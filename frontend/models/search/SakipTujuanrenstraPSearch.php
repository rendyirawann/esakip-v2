<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipTujuanrenstraP;

/**
 * SakipTujuanrenstraSearch represents the model behind the search form of `frontend\models\SakipTujuanrenstra`.
 */
class SakipTujuanrenstraPSearch extends SakipTujuanrenstraP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reftujuanrenstra_id', 'reftujuanrenstra_p_id', 'refskpd_id', 'refmisi_p_id', 'refsasaranrenstra_p_id', 'refsasaran_p_id', 'reftujuan_p_id', 'refperiode_5tahun_id'], 'integer'],
            [['user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'safe'],
            [['uraian_tujuanrenstra_p'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipTujuanrenstraP::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'misi']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'reftujuanrenstra_p_id' => $this->reftujuanrenstra_p_id,
            'reftujuanrenstra_id' => $this->reftujuanrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refmisi_p_id' => $this->refmisi_p_id,
            'refsasaranrenstra_p_id' => $this->refsasaranrenstra_p_id,
            'refsasaran_p_id' => $this->refsasaran_p_id,
            'reftujuan_p_id' => $this->reftujuan_p_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_tujuanrenstra_p', $this->uraian_tujuanrenstra_p])
            ->andFilterWhere(['like', 'user_create', $this->user_create])
            ->andFilterWhere(['like', 'date_create', $this->date_create])
            ->andFilterWhere(['like', 'user_edit', $this->user_edit])
            ->andFilterWhere(['like', 'date_edit', $this->date_edit])
            ->andFilterWhere(['like', 'user_delete', $this->user_delete])
            ->andFilterWhere(['like', 'date_delete', $this->date_delete]);

        return $dataProvider;
    }
}
