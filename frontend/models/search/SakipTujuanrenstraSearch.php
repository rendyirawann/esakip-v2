<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipTujuanrenstra;

/**
 * SakipTujuanrenstraSearch represents the model behind the search form of `frontend\models\SakipTujuanrenstra`.
 */
class SakipTujuanrenstraSearch extends SakipTujuanrenstra
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reftujuanrenstra_id', 'refskpd_id', 'refmisi_id', 'refsasaranrenstra_id', 'refsasaran_id', 'reftujuan_id', 'refperiode_5tahun_id'], 'integer'],
            [['user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'safe'],
            [['uraian_tujuanrenstra'], 'string'],
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
        $query = SakipTujuanrenstra::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'misi', 'visi']);

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
            'reftujuanrenstra_id' => $this->reftujuanrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refmisi_id' => $this->refmisi_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refsasaran_id' => $this->refsasaran_id,
            'reftujuan_id' => $this->reftujuan_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_tujuanrenstra', $this->uraian_tujuanrenstra])
            ->andFilterWhere(['like', 'user_create', $this->user_create])
            ->andFilterWhere(['like', 'date_create', $this->date_create])
            ->andFilterWhere(['like', 'user_edit', $this->user_edit])
            ->andFilterWhere(['like', 'date_edit', $this->date_edit])
            ->andFilterWhere(['like', 'user_delete', $this->user_delete])
            ->andFilterWhere(['like', 'date_delete', $this->date_delete]);

        return $dataProvider;
    }
}
