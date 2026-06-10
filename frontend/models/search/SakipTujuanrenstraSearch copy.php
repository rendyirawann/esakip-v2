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
            [['reftujuanrenstra_id', 'refskpd_id', 'refmisi_id', 'reftujuan_id', 'refsasaranrenstra_id', 'refsasaran_id', 'refperiode_id'], 'integer'],
            [['uraian_tujuanrenstra', 'user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete', 'tujuanrenstra_isaktif'], 'safe'],
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
            'reftujuan_id' => $this->reftujuan_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refsasaran_id' => $this->refsasaran_id,
            'refperiode_id' => $this->refperiode_id,
            'date_create' => $this->date_create,
            'date_edit' => $this->date_edit,
            'date_delete' => $this->date_delete,
        ]);

        $query->andFilterWhere(['like', 'uraian_tujuanrenstra', $this->uraian_tujuanrenstra])
            ->andFilterWhere(['like', 'user_create', $this->user_create])
            ->andFilterWhere(['like', 'user_edit', $this->user_edit])
            ->andFilterWhere(['like', 'user_delete', $this->user_delete])
            ->andFilterWhere(['like', 'tujuanrenstra_isaktif', $this->tujuanrenstra_isaktif]);

        return $dataProvider;
    }
}
